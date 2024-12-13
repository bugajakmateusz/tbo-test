import { Injectable } from '@angular/core';
import { Machine } from '../models/machine.model';
import { Snack } from 'src/app/snacks/models/snack/snack.model';
import { SnackInMachine } from '../models/snack-in-machine.model';

@Injectable({
  providedIn: 'root',
})
export class MachinesService {
  machines: Machine[] = [
    {
      id: '1',
      name: 'some machine',
      note: 'some note about a machine',
      active: true,
    },
    {
      id: '2',
      name: 'other machine',
      note: 'some note about a machine',
      active: false,
    },
  ];

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
  constructor() {}

  editMachine(name: string, note: string) {
    console.log(
      `edit machine with ID: ${this.id}. New name: ${name}. New note: ${note}`
    );
  }

  activateDeactivateMachine() {
    console.log(`activate/deactivate machine with ID: ${this.id}`);
  }

  addMachine(name: string, note: string) {
    console.log(`add machine. Name: ${name}. Note: ${note}`);
  }

  getMachines() {
    return this.machines;
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
