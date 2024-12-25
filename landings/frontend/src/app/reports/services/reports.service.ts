import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root',
})
export class ReportsService {
  createBuyReport(dateFrom: string, dateTo: string) {
    console.log(
      `create buy report. Date from: ${dateFrom}. Date to: ${dateTo}`
    );
  }

  createSellReport(dateFrom: string, dateTo: string, machines: string[]) {
    console.log(
        `create sell report. Date from: ${dateFrom}. Date to: ${dateTo}. Machines: ${machines}`
    );
  }

  createWarehouseReport(dateFrom: string, dateTo: string) {
    console.log(
      `create warehouse report. Date from: ${dateFrom}. Date to: ${dateTo}`
    );
  }

  createMachinesReport(dateFrom: string, dateTo: string, machines: string[]) {
    console.log(
      `create machines report. Date from: ${dateFrom}. Date to: ${dateTo}. Machines: ${machines}`
    );
    console.log({
      dateFrom: dateFrom,
      dateTo: dateTo,
      machines: machines
    })
  }
}


