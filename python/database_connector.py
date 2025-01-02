import os
import psycopg2
import pandas as pd
from dotenv import load_dotenv

class DatabaseConnector:
    def __init__(self):
        load_dotenv()
        self.conn = psycopg2.connect(
            host=os.getenv("DB_HOST"),
            database=os.getenv("DB_NAME"),
            user=os.getenv("DB_USER"),
            password=os.getenv("DB_PASS")
        )

    def get_data(self, query):
        return pd.read_sql_query(query, self.conn)

    def fetch_data(self, query):
        data = self.get_data(query)
        data = data.astype(str)

        table_data = [tuple(data.columns)] + list(data.itertuples(index=False, name=None))

        return tuple(table_data)

    def close(self):
        self.conn.close()