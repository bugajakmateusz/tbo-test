import { Injectable } from '@angular/core';
import { Machine } from '../models/machine.model';

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
}
