import { Injectable } from '@angular/core';
import { MachineDisplayed } from '../models/machine-displayed.model';
import { Machine } from '../models/machine.model';
import { SnackInMachine } from '../models/snack-in-machine.model';
import { Snack } from 'src/app/snacks/models/snack/snack.model';
import { SnackInMachineDisplayed } from '../models/snack-in-machine-displayed.model';
import { MachineSimpleDisplayed } from '../models/machine-simple-displayed.model';

@Injectable({
  providedIn: 'root',
})
export class MachinesMapperService {
  mapMachineToMachineDisplayed(machine: Machine): MachineDisplayed {
    const { id, name } = machine;
    const active = machine.active ? 'Tak' : 'Nie';
    return { id, name, active };
  }

  mapMachineToMachineSimpleDisplayed(machine: Machine): MachineSimpleDisplayed {
    const { id, name } = machine;
    return { id, name };
  }

  mapSnackInMachineToSnackInMachineDisplayed(
    snack: SnackInMachine
  ): SnackInMachineDisplayed {
    const { id, name } = snack;
    return { id, name };
  }
}
