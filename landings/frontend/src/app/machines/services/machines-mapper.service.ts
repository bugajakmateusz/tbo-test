import { Injectable } from '@angular/core';
import { Machine } from '../models/machine.model';
import { SnackInMachine } from '../models/snack-in-machine.model';
import { Snack } from 'src/app/snacks/models/snack/snack.model';
import { SnackInMachineDisplayed } from '../models/snack-in-machine-displayed.model';
import { MachineSimpleDisplayed } from '../models/machine-simple-displayed.model';
import {MachineFromApi} from "../models/machine-from-api.model";

@Injectable({
  providedIn: 'root',
})
export class MachinesMapperService {

  mapMachineToMachineSimpleDisplayed(machine: Machine): MachineSimpleDisplayed {
    const { id, location } = machine;
    return { id, location };
  }

  mapSnackInMachineToSnackInMachineDisplayed(
    snack: SnackInMachine
  ): SnackInMachineDisplayed {
    const { id, name } = snack;
    return { id, name };
  }

  mapMachineFromApiToMachine(machineFromApi: MachineFromApi): Machine {
    const {id} = machineFromApi
    const location = machineFromApi.attributes.location
    const positionsNumber = machineFromApi.attributes.positionsNumber
    const positionsCapacity = machineFromApi.attributes.positionsCapacity
    return {id, location, positionsNumber, positionsCapacity}
  }
}
