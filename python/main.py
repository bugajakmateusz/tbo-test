from database_connector import DatabaseConnector
from report_generator import ReportGenerator
from email_sender import EmailSender
from apscheduler.schedulers.blocking import BlockingScheduler

def job():
    db_connector = DatabaseConnector()
    report_gen = ReportGenerator(db_connector)
    email_sender = EmailSender()

    top_daily = report_gen.get_top_selling_snacks(1)
    top_weekly = report_gen.get_top_selling_snacks(7)
    shortages = report_gen.get_warehouse_shortages()
    sold_sum = report_gen.get_sold_sum()
    report_gen.plot_sales_trend()
    report_gen.generate_pdf_report(top_daily, top_weekly, shortages, sold_sum)

    body = """
    <h1>Daily Report</h1>
    <p>Please find the attached PDF with the detailed vending machine report.</p>
    <p>Best regards,<br>Vending Management System</p>
    """

    email_sender.send_email(
        recipient="management@vending.com",
        subject="Daily Vending Report",
        body=body,
        attachment='daily_report.pdf'
    )

    db_connector.close()

scheduler = BlockingScheduler()
scheduler.add_job(job, 'cron', hour=9)
scheduler.start()

# job()