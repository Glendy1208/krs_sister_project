FROM python:3.10-slim

WORKDIR /temp_bank

COPY . /temp_bank

RUN pip install -r requirements.txt

EXPOSE 5001

CMD ["flask", "run", "--host=0.0.0.0", "--port=5001"]
