FROM python:3.10-slim

WORKDIR /temp

COPY . /temp

RUN pip install -r requirements.txt

EXPOSE 5000

ENV FLASK_APP=app.py
ENV FLASK_ENV=development

COPY wait-for-it.sh /wait-for-it.sh
RUN chmod +x /wait-for-it.sh

CMD ["./wait-for-it.sh", "mysql:3306", "--timeout=30", "--", "python", "app.py"]
