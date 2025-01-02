import requests
import matplotlib.pyplot as plt
import pandas as pd
from fpdf import FPDF

class ReportGenerator:
    def __init__(self, db_connector):
        self.db_connector = db_connector

    def get_top_selling_snacks(self, days=1):
        query = f"""
        SELECT s.name, COUNT(*) as total_sold
        FROM snack_sells ss
        JOIN prices_history ph ON ss.price_id = ph.price_id
        JOIN snacks s ON ph.snack_id = s.snack_id
        WHERE ss.sold_at > NOW() - INTERVAL '{days} days'
        GROUP BY s.name
        ORDER BY total_sold DESC
        LIMIT 5
        """
        return self.db_connector.fetch_data(query)

    def get_warehouse_shortages(self):
        query = """
        SELECT s.name, ws.quantity
        FROM warehouse_snacks ws
        JOIN snacks s ON ws.snack_id = s.snack_id
        WHERE ws.quantity = 0
        """
        return self.db_connector.fetch_data(query)

    def get_exchange_rate(self, date):
        date_str = date.strftime('%Y-%m-%d')
        url = f'https://api.nbp.pl/api/exchangerates/rates/A/EUR/{date_str}/?format=json'
        response = requests.get(url)

        if response.status_code == 200:
            return response.json()['rates'][0]['mid']  # Kurs średni
        else:
            return None  # Jeśli nie znajdzie kursu, zwróć None

    def get_sold_sum(self):
        query = """
        SELECT DATE(ss.sold_at) as sell_date, SUM(ph.price) as total_sold
        FROM snack_sells ss
        JOIN prices_history ph ON ss.price_id = ph.price_id
        WHERE ss.sold_at > NOW() - INTERVAL '7 days'
        GROUP BY sell_date
        ORDER BY sell_date
        """
        data = self.db_connector.get_data(query)
        data['sell_date'] = pd.to_datetime(data['sell_date'])
        data['total_sold'] = data['total_sold'].astype(float)
        data['total_sold_eur'] = data['sell_date'].apply(lambda x: self.convert_to_eur(x, data))
        data = data.astype(str)

        table_data = [tuple(data.columns)] + list(data.itertuples(index=False, name=None))
        return tuple(table_data)

    def convert_to_eur(self, date, data):
        rate = self.get_exchange_rate(date)
        if rate:
            return round(data[data['sell_date'] == date]['total_sold'].values[0] / rate, 2)
        else:
            return 'N/A'  # Jeśli kurs nie jest dostępny

    def plot_sales_trend(self):
        query = """
        SELECT DATE(ss.sold_at) as sell_date, SUM(ph.price) as total_sold
        FROM snack_sells ss
        JOIN prices_history ph ON ss.price_id = ph.price_id
        WHERE ss.sold_at > NOW() - INTERVAL '7 days'
        GROUP BY sell_date
        ORDER BY sell_date
        """
        df = self.db_connector.get_data(query)
        plt.figure(figsize=(10, 6))
        plt.scatter(df['sell_date'], df['total_sold'])
        plt.plot(df['sell_date'], df['total_sold'], linestyle='--')
        plt.title('Daily Sales Trend (Last 7 days)')
        plt.xlabel('Date')
        plt.ylabel('Total Sold')
        plt.grid(True)
        plt.savefig('sales_trend.png')
        plt.close()

    def generate_pdf_report(self, top_daily, top_weekly, shortages, sold_sum):
        pdf = FPDF()
        pdf.set_auto_page_break(auto=True, margin=15)
        pdf.add_page()
        pdf.set_font("Arial", size=12)
        pdf.cell(200, 10, txt="Daily Vending Report", ln=True, align='C')
        pdf.ln(10)

        pdf.cell(200, 10, txt="Top 5 Snacks (Yesterday)", ln=True)
        pdf.ln(5)
        with pdf.table() as table:
            for data_row in top_daily:
                row = table.row()
                for datum in data_row:
                    row.cell(datum)
        pdf.ln(10)

        pdf.cell(200, 10, txt="Top 5 Snacks (Last 7 days)", ln=True)
        pdf.ln(5)

        with pdf.table() as table:
            for data_row in top_weekly:
                row = table.row()
                for datum in data_row:
                    row.cell(datum)
        pdf.ln(10)

        pdf.cell(200, 10, txt="Warehouse Shortages", ln=True)
        pdf.ln(5)

        with pdf.table() as table:
            for data_row in shortages:
                row = table.row()
                for datum in data_row:
                    row.cell(datum)
        pdf.ln(10)

        pdf.cell(200, 10, txt="Sold income", ln=True)
        pdf.ln(5)

        with pdf.table() as table:
            for data_row in sold_sum:
                row = table.row()
                for datum in data_row:
                    row.cell(datum)
        pdf.ln(10)

        pdf.image('sales_trend.png', x=10, w=190)
        pdf.output("daily_report.pdf")