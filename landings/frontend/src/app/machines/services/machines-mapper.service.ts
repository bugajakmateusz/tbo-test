import { Injectable } from '@angular/core';
import { MachineDisplayed } from '../models/machine-displayed.model';
import { Machine } from '../models/machine.model';

@Injectable({
  providedIn: 'root',
})
export class MachinesMapperService {
  mapMachineToMachineDisplayed(machine: Machine): MachineDisplayed {
    const { id, name } = machine;
    const active = machine.active ? 'Tak' : 'Nie';
    return { id, name, active };
  }
}
