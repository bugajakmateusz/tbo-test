import { Injectable } from '@angular/core';
import {SnackFromApi} from "../models/snack-from-api.model";
import {Snack} from "../models/snack/snack.model";

@Injectable({
  providedIn: 'root'
})
export class SnacksMapperService {
  mapSnackFromApiToSnack(snackFromApi: SnackFromApi): Snack {
    const {id} = snackFromApi
    const name = snackFromApi.attributes.name
    return {id, name}
  }
}
