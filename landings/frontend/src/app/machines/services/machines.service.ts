import { Injectable } from '@angular/core';
import { Machine } from '../models/machine.model';
import { Snack } from 'src/app/snacks/models/snack/snack.model';
import { SnackInMachine } from '../models/snack-in-machine.model';
import { Observable, map } from 'rxjs';
import { HttpClient } from '@angular/common/http';
import {MachinesMapperService} from "./machines-mapper.service";
import {MachineFromApi} from "../models/machine-from-api.model";

@Injectable({
  providedIn: 'root',
})
export class MachinesService {
  machines: Machine[] = [];

  snacks: SnackInMachine[] = [
    {
      id: '1',
      name: 'some snack',
      price: 2,
    },
    {
      id: '2',
      name: 'other snack',
      price: 3,
    },
  ];
  action = '';
  id = '';

  snackInMachineId = '';

  constructor(private http: HttpClient, private machinesMapperService: MachinesMapperService) {
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
          positionsNumber: positionsNumber,
          positionsCapacity: positionsCapacity
        }
      }
    })
        .subscribe(data => console.log(data))
    this.updateServiceData()
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

  getCurrentMachine() {
    return this.getMachine(this.id);
  }

  getSnacks(machineId: string) {
    return this.snacks;
  }
}
