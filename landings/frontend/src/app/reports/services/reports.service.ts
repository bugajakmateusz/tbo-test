import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class ReportsService {
  createBuySellReport(dateFrom: string, dateTo: string) {
    console.log(
      `create buy/sell report. Date from: ${dateFrom}. Date to: ${dateTo}`
    );
  }

  createWarehouseReport(dateFrom: string, dateTo: string) {
    console.log(
      `create warehouse report. Date from: ${dateFrom}. Date to: ${dateTo}`
    );
  }

  createMachinesReport(dateFrom: string, dateTo: string) {
    console.log(
      `create machines report. Date from: ${dateFrom}. Date to: ${dateTo}`
    );
  }
}
