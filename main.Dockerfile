FROM python:3-slim
WORKDIR /usr/src/app
COPY requirements.txt ./
RUN pip install --no-cache-dir -r requirements.txt
COPY ./main.py ./invokes.py ./
CMD [ "python", "./main.py" ]