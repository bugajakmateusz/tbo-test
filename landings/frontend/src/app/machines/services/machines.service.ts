import { Injectable } from '@angular/core';
import { Machine } from '../models/machine.model';
import { Snack } from 'src/app/snacks/models/snack/snack.model';
import { SnackInMachine } from '../models/snack-in-machine.model';
import { Observable, map } from 'rxjs';
import { HttpClient } from '@angular/common/http';

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
  constructor(private http: HttpClient) {}

  editMachine(name: string, note: string) {
    console.log(
      `edit machine with ID: ${this.id}. New name: ${name}. New note: ${note}`
    );
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

  addMachine(name: string, note: string) {
    console.log(`add machine. Name: ${name}. Note: ${note}`);
  }

  getMachines(): Observable<Machine[]> {
    return this.http
      .get<Machine[]>(`http://localhost:3100/api/json-api/machines`)
      .pipe(
        map((response) => {
          if (response) {
            console.log(response);
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
