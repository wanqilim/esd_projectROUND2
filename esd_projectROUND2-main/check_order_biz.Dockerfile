FROM python:3-slim
WORKDIR /usr/src/app
COPY requirements.txt ./
RUN pip install --no-cache-dir -r requirements.txt
COPY ./check_order_biz.py ./invokes.py ./amqp_setup.py ./
CMD [ "python", "./check_order_biz.py" ]