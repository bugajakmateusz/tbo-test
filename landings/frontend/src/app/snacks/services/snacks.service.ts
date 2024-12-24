import { Injectable } from '@angular/core';
import { Snack } from '../models/snack/snack.model';
import {ConfigService} from "../../config.service";
import { HttpClient } from '@angular/common/http';
import {map, Observable} from "rxjs";
import {SnackFromApi} from "../models/snack-from-api.model";
import {SnacksMapperService} from "./snacks-mapper.service";


@Injectable({
  providedIn: 'root',
})
export class SnacksService {
  snacks: Snack[] = [
    {
      id: '1',
      name: 'some snack',
    },
    {
      id: '2',
      name: 'other snack',
    },
  ];

  action = '';
  id = '';
  constructor(private http: HttpClient, private configService: ConfigService, private snacksMapperService: SnacksMapperService) {
    this.updateServiceData()
  }

  private updateServiceData() {
    this.getSnacks().subscribe(snacksFromApi => this.snacks = snacksFromApi.map(snackFromApi => this.snacksMapperService.mapSnackFromApiToSnack(snackFromApi)))
  }


  editSnack(name: string) {
    console.log(`edit snack with ID: ${this.id}. New name: ${name}`);
  }

  deleteSnack() {
    console.log(`delete snack with ID: ${this.id}`);
  }

  addSnack(name: string) {
    console.log(name)
this.http.post(`${this.configService.apiUrl}json-api/snacks`, {
  data: {
    type: "snacks",
    attributes: {
      name: name
    }
  }
})
    .subscribe(data => console.log(data))
  }

  getSnacks(): Observable<SnackFromApi[]> {
    return this.http
        .get<any>(`${this.configService.apiUrl}json-api/snacks`)
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

  getSnack(id: string) {
    return this.snacks.filter((el: Snack) => el.id === id)[0];
  }

  getCurrentSnack() {
    return this.getSnack(this.id);
  }
}
