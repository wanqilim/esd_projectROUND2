FROM python:3-slim
WORKDIR /usr/src/app
COPY requirements.txt ./
RUN pip install --no-cache-dir -r requirements.txt
COPY ./fulfill_order.py ./invokes.py ./amqp_setup.py ./
CMD [ "python", "./fulfill_order.py" ]