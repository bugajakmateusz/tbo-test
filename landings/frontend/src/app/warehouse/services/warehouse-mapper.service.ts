import { Injectable } from '@angular/core';
import {WarehouseSnackFromApi} from "../models/warehouse-snack-from-api.model";
import {WarehouseSnack} from "../models/warehouseSnack.model";

@Injectable({
  providedIn: 'root'
})
export class WarehouseMapperService {

  mapWarehouseSnackFromApiToWarehouseSnack(snackFromApi: WarehouseSnackFromApi): WarehouseSnack {
    const {id} = snackFromApi
    const name = snackFromApi.attributes.name
    const amount = snackFromApi.attributes.quantity
    return {id, name, amount}
  }
}
