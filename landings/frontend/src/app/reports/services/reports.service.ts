import { Injectable } from '@angular/core';
import {HttpClient} from "@angular/common/http";
import {ConfigService} from "../../config.service";

@Injectable({
  providedIn: 'root',
})
export class ReportsService {

  constructor(private http: HttpClient, private configService: ConfigService) {
  }
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
      `create machines report. Datte from: ${dateFrom}. Date to: ${dateTo}. Machines: ${machines}`
    );
    console.log({
      dateFrom: dateFrom,
      dateTo: dateTo,
      machines: machines
    })

    this.http.post(`${this.configService.apiUrl}reports/machines`,{
      machineIds: machines
    })
      .subscribe((data: any) => {
        console.log(data)
        })

  }
}


