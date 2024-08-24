from typing import Union, List
from fastapi import FastAPI, File, UploadFile, HTTPException, Form
from pydantic import BaseModel
from zhipuai import ZhipuAI
import base64
import urllib.parse
import requests
import aiofiles #有人可能没有！！！
import logging
import time
import os 
import uvicorn
from tempfile import NamedTemporaryFile

# 初始化FastAPI应用
app = FastAPI()

# 初始化日志记录
logging.basicConfig(level=logging.INFO)

# 初始化质普清言API客户端
client = ZhipuAI(api_key="c2ecb49cb11170a5d0ad907809cf40a2.WlsWODNQRB8aob2Y")  # 填写您自己的APIKey

class AnswerRequest(BaseModel):
    text: str
    context: str

class SentenceEmbeddingRequest(BaseModel):
    text:str
class TextSegmentRequest(BaseModel):
    text: str
from fastapi import FastAPI, UploadFile, Form
import base64
import urllib.parse
import requests

app = FastAPI()

API_KEY = "MMuUddX56uUw1ZUfkdjcyVS7"#之后会填入
SECRET_KEY = "XZwu3tRkobh5SAq7PzGoMpzS00UR6zbz"#之后会填入

def get_access_token():
    url = "https://aip.baidubce.com/oauth/2.0/token"
    params = {
        "grant_type": "client_credentials",
        "client_id": API_KEY,
        "client_secret": SECRET_KEY
    }
    response = requests.get(url, params=params)
    access_token = str(response.json().get("access_token"))
    print("Access Token:", access_token)  # 打印access_token
    return access_token

def get_file_content_as_base64(file: UploadFile, urlencoded=False):
    content = base64.b64encode(file.file.read()).decode("utf8")
    if urlencoded:
        content = urllib.parse.quote_plus(content)
    return content

@app.post("/ocr/")
async def ocr(file: UploadFile, url: str = Form(None)):
    access_token = get_access_token()
    ocr_url = f"https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic?access_token={access_token}"
    
    image_base64 = get_file_content_as_base64(file, urlencoded=True)
    payload = f"image={image_base64}&url={urllib.parse.quote_plus(url)}&language_type=CHN_ENG&detect_direction=false&detect_language=false&paragraph=false&probability=false"
    
    headers = {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
    }
    
    response = requests.post(ocr_url, headers=headers, data=payload)
    ocr_result=response.json()
    return {"access_token": access_token, "ocr_result": ocr_result}


@app.get("/")
def read_root():
    return {"Hello": "World"}

@app.post("/answerquestion/") # 整个问答的最后一步，设定一个prompt然后得到一个答案
def answerquestion(request: AnswerRequest):
    try:
        logging.info(f"Received answer request: {request}")

        # 构造翻译请求
        if request.context:
            message = f"你是一个化妆品专家，了解化妆品的化学成分以及里面对人皮肤的作用或者危害。请基于我给你提供的我的基本信息来有针对性的一项一项地回答我的问题:\n{request.context}\n回答下面的问题:\n{request.text}\n"
        else:
            message = f"你是一个化妆品专家，了解化妆品的化学成分以及里面对人皮肤的作用或者危害。请直接回答下面的问题:\n{request.text}\n"
        
        response = client.chat.completions.create(
            model="glm-4",  # 填写需要调用的模型名称
            messages=[
                {"role": "user", "content": message}
            ],
        )
        answer = response.choices[0].message.content

        logging.info(f"answer result: {answer}")

        return {"question": request.text, "answer": answer,"context":request.context}
    except Exception as e:
        logging.error(f"Error occurred while answer: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/process-txt/")
async def process_txt(file: UploadFile = File(...)):#UploadFile 是 FastAPI 提供的文件上传类，用于接收上传的文件数据。
    try:
        logging.info(f"Received file: {file.filename}")

        # 将 SpooledTemporaryFile 写入一个临时文件
        temp_file = NamedTemporaryFile(delete=False)
        try:
            temp_file.write(await file.read())
            temp_file.close()

            # 读取临时文件内容
            async with aiofiles.open(temp_file.name, 'r', encoding='utf-8') as f:
                content = await f.read()
        finally:
            os.remove(temp_file.name)#删除临时文件

        # 按换行符分割文本
        segments = content.split('\n')

        source_embeddings = []

        # 逐段发送文本内容到ZhipuAI进行向量化
        for segment in segments:
            if segment.strip():  # 跳过空行
                source_response = client.embeddings.create(
                    model="embedding-2",
                    input=segment.strip(),
                )
                source_embedding = source_response.data[0].embedding
                source_embeddings.append(source_embedding)
                logging.info(f"Processed segment: {segment.strip()}")
                logging.info(f"Source embedding: {source_embedding}")

        # 返回所有段落的向量
        return {"source_embeddings": source_embeddings, "segments": segments}
    except Exception as e:
        logging.error(f"Error occurred while processing TXT file: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))
        
@app.post("/process-pictext/")
async def process_pictext(segments: List[str]):
    source_embedding = []
    for segment in segments:
        source_response = client.embeddings.create(
            model="embedding-2",
            input=segment,)
        current_embedding = source_response.data[0].embedding
        source_embedding.append(current_embedding)
        logging.info(f"Processed segment: {segment.strip()}")
        logging.info(f"Source embedding: {current_embedding}")
    return {"source_embeddings": source_embedding, "segments": segments}
    

@app.post("/get-sentence-embedding/")#把这个函数变为需要处理POST请求的函数
async def get_sentence_embedding(request: SentenceEmbeddingRequest):#说明函数的内容，并且request根据最前面类的定义，需要一个叫“text的str
    try:#执行一段代码
        logging.info(f"Received sentence embedding request: {request}")#logging.info接受request

        text = request.text#获取request请求中text的值

        response = client.embeddings.create(
            model="embedding-2",
            input=text,
        )
        embedding = response.data[0].embedding

        logging.info(f"Sentence embedding: {embedding}")

        return {"embedding": embedding}
    except Exception as e:
        logging.error(f"Error occurred while getting sentence embedding: {str(e)}")
        raise HTTPException(status_code=500, detail=str(e))