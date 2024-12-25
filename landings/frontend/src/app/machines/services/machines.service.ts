import { Injectable } from '@angular/core';
import { Machine } from '../models/machine.model';
import { Snack } from '../../snacks/models/snack/snack.model';
import { SnackInMachine } from '../models/snack-in-machine.model';
import { Observable, map } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import {MachinesMapperService} from "./machines-mapper.service";
import {MachineFromApi} from "../models/machine-from-api.model";
import {ConfigService} from "../../config.service";
import {SnackInMachineDisplayed} from "../models/snack-in-machine-displayed.model";

@Injectable({
  providedIn: 'root',
})
export class MachinesService {
  machines: Machine[] = [];

  snacks: SnackInMachineDisplayed[] = [];
  action = '';
  id = '';

  snackInMachineId = '';

  constructor(private http: HttpClient, private machinesMapperService: MachinesMapperService, private configService: ConfigService) {
    this.login();
    this.updateServiceData()
  }

  private login() {
    this.http
        .post('http://localhost:3100/api/login', {
          username: 'ebaranowski@onet.pl',
          password: 'tab-admin',
        })
        .subscribe((data) => {
          console.log(data);
        });
  }

  private updateServiceData() {
    this.getMachines().subscribe(machinesFromApi => this.machines = machinesFromApi.map(machineFromApi => this.machinesMapperService.mapMachineFromApiToMachine(machineFromApi)))
  }

  editMachine(location: string, positionsNumber: string, positionsCapacity: string) {
    this.http.patch(`http://localhost:3100/api/json-api/machines/${this.id}`, {
      data: {
        type: "machines",
        attributes: {
          location: location,
          positionsNumber: Number(positionsNumber),
          positionsCapacity: Number(positionsCapacity)
        }
      }
    })
        .subscribe(data => console.log(data))
    this.updateServiceData()
  }

  changePrice(price: string) {
    this.addSnackToMachine(this.snackInMachineId, price)
    this.getMachineFromApi()
  }

  addSnackToMachine(snackId: string, price: string) {
      this.http.post(`${this.configService.apiUrl}json-api/snacks-prices`, {
    data: {
      type: "snacks-prices",
      attributes: {
          price: price
      },
      relationships: {
        machine: {
          data: {
            type: "machines",
            id: this.id
          }
        },
        snack: {
          data: {
            type: "snacks",
            id: snackId
          }
        }
      }
      }})
          .subscribe(data => console.log(data))
  }

  activateDeactivateMachine() {
    console.log(`activate/deactivate machine with ID: ${this.id}`);
  }

  changePricesInMachine(updatedPrices: any) {
    console.log(`change prices in machine with ID: ${this.id}. New prices:`);
    console.log(updatedPrices);
    // Send updatedPrices to the backend via an HTTP request
    // Example: this.snackService.updatePrices(updatedPrices).subscribe(...)
  }

  addMachine(location: string, positionsNumber: string, positionsCapacity: string) {
    this.http.post(`http://localhost:3100/api/json-api/machines`, {
      data: {
        type: "machines",
        attributes: {
          location: location,
          positionsNumber: positionsNumber,
          positionsCapacity: positionsCapacity
        }
      }
    })
        .subscribe(data => console.log(data))
  }
  getMachines(): Observable<MachineFromApi[]> {
    return this.http
      .get<any>(`http://localhost:3100/api/json-api/machines?fields%5Bmachines%5D=location%2CpositionsNumber%2CpositionsCapacity`)
      .pipe(
        map((response) => {
          if (response) {
            return response.data
          }
          return []; // If response is null return empty array for safety.
        })
      );
  }

  getMachine(id: string) {
    return this.machines.filter((el: Machine) => el.id === id)[0];
  }

  getMachineFromApi(): Observable<any> {
    // json-api/machines/15?fields[machines]=location,positionsNumber,positionsCapacity,machineSnacks&include=machineSnacks,machineSnacks.snack&fields[machine-snacks]=quantity,position,snack,price&fields[snacks]=name
    return this.http
        .get<any>(`http://localhost:3100/api/json-api/machines/${this.id}?fields[machines]=location,positionsNumber,positionsCapacity,snacksPrices&include=snacksPrices,snacksPrices.snack&fields[snacks-prices]=price,snack&fields[snacks]=name`)
        .pipe(
            map((response) => {
              if (response) {
                this.snacks = []
                response.included.filter((el:any) => el.type === "snacks").forEach((snack:any) => {
                  let newSnack: SnackInMachineDisplayed = {id: snack.id, name: snack.attributes.name, price: "1"}
                  this.snacks.push(newSnack)
                })
                response.included.filter((el:any) => el.type === "snacks-prices").forEach((snackPrice:any) => {
                  this.snacks.find(snack => snack.id === snackPrice.relationships.snack.data.id)!.price = snackPrice.attributes.price
                })
                return response
              }
              return []; // If response is null return empty array for safety.
            })
        );
  }

  getCurrentMachine() {
    return this.getMachine(this.id);
  }

  getCurrentSnackPrice() {
    return this.getSnackPrice(this.snackInMachineId)
  }

  getSnackPrice(id: string) {
    return this.snacks.filter((el) => el.id === id)[0];
  }
  getSnacks(machineId: string) {
    return this.snacks;
  }
}
