import { Component } from '@angular/core';
import { Machine } from '../../models/machine.model';
import { MachineDisplayed } from '../../models/machine-displayed.model';
import { FormBuilder } from '@angular/forms';
import { MachinesService } from '../../services/machines.service';
import { MachinesMapperService } from '../../services/machines-mapper.service';
import { MachineChangePriceDisplayed } from '../../models/machine-change-price-displayed.model';
import { SnackInMachine } from '../../models/snack-in-machine.model';

@Component({
  selector: 'app-change-prices-page',
  templateUrl: './change-prices-page.component.html',
  styleUrls: ['./change-prices-page.component.scss'],
})
export class ChangePricesPageComponent {
  machinesListcolumns = ['ID', 'Nazwa'];
  snacksListcolumns = ['ID', 'Nazwa', 'Cena w maszynie'];

  machines: Machine[] = [];

  snacksInMachine: SnackInMachine[] = [
    {
      id: '1',
      name: 'init snack',
      price: 69,
    },
  ];

  showMachines: boolean = true;

  displayedMachines: MachineChangePriceDisplayed[] = [];

  buttons = [{ text: 'Wybierz', action: 'chooseMachine' }];

  constructor(
    private fb: FormBuilder,
    private machinesService: MachinesService,
    private machinesMapperService: MachinesMapperService
  ) {}

  ngOnInit() {
    this.machines = this.machinesService.getMachines();
    this.displayedMachines = this.machines.map((el) =>
      this.machinesMapperService.mapMachineToMachineChangePriceDisplayed(el)
    );
  }

  onMachineChosen(event: { id: string; action: string }) {
    this.snacksInMachine = this.machinesService.getSnacks(event.id);
    this.showMachines = false;
  }

  goBack() {
    this.showMachines = true;
  }
}
