import { Injectable } from '@angular/core';
import { WarehouseSnack } from '../models/warehouseSnack.model';
import {HttpClient} from "@angular/common/http";
import {ConfigService} from "../../config.service";
import {WarehouseSnackFromApi} from "../models/warehouse-snack-from-api.model";
import {map, Observable} from "rxjs";
import {WarehouseMapperService} from "./warehouse-mapper.service";

@Injectable({
  providedIn: 'root',
})
export class WarehouseService {
  snacks: WarehouseSnack[] = []

  constructor(private httpClient: HttpClient, private configService: ConfigService, private warehouseMapperService: WarehouseMapperService) {
    this.updateServiceData()
  }

  private updateServiceData() {
    this.getSnacks().subscribe(snacksFromApi => this.snacks = snacksFromApi.map(snackFromApi => this.warehouseMapperService.mapWarehouseSnackFromApiToWarehouseSnack(snackFromApi)))
  }
  getSnacks(): Observable<WarehouseSnackFromApi[]> {
    return this.httpClient.get<any>(`${this.configService.apiUrl}json-api/snacks`)
        .pipe(
            map((response) => {
              if (response) {
                console.log(response.data)
                return response.data
              }
              return []; // If response is null return empty array for safety.
            })
        );
  }

  acceptDelivery(delivery: any) {
    console.log('accept delivery. Snacks accepted: ');
    console.log(delivery);
  }

  handToCourier(snacks: any) {
    console.log('hand to courier. Snacks handed: ');
    console.log(snacks);
  }
}
