FROM python:3.11-alpine

COPY ../python /app
COPY ./docker/python/requirements.txt /app/requirements.txt

WORKDIR /app

RUN \
 apk add --no-cache g++ gcc libstdc++ clang postgresql-libs && \
 apk add --no-cache --virtual .build-deps gcc musl-dev postgresql-dev && \
 python3 -m pip install -r requirements.txt --no-cache-dir && \
 apk --purge del .build-deps

CMD ["python", "main.py"]