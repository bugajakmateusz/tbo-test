import { Component, OnInit } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { MachinesService } from '../../services/machines.service';
import { Machine } from '../../models/machine.model';
import { MachineDisplayed } from '../../models/machine-displayed.model';
import { MachinesMapperService } from '../../services/machines-mapper.service';
import { SnackInMachine } from '../../models/snack-in-machine.model';
import { SnackInMachineDisplayed } from '../../models/snack-in-machine-displayed.model';

@Component({
  selector: 'app-view-machines-page',
  templateUrl: './view-machines-page.component.html',
  styleUrls: ['./view-machines-page.component.scss'],
})
export class ViewMachinesPageComponent implements OnInit {
  machinesListcolumns = ['ID', 'Nazwa', 'Aktywna'];
  snacksListcolumns = ['ID', 'Nazwa'];

  machines: Machine[] = [];

  displayedMachines: MachineDisplayed[] = [];

  snacksInMachine: SnackInMachine[] = [
    {
      id: '1',
      name: 'init snack',
      price: 69,
    },
  ];

  snacksInMachineDisplayed: SnackInMachineDisplayed[] = [];

  showMachines: boolean = true;

  chosenMachineName = '';

  machinesListButtons = [
    { text: 'Edytuj', action: 'editMachine' },
    { text: 'Aktywuj', action: 'activate/deactivateMachine' },
    {
      text: 'Ceny',
      action: 'changePrices',
    },
  ];

  snacksListInputs = [
    {
      title: 'Cena w maszynie',
      name: 'snack',
      type: 'number',
    },
  ];

  form = this.fb.group({
    name: [''],
    note: [''],
  });

  snacksForm = this.fb.group({});

  constructor(
    private fb: FormBuilder,
    private machinesService: MachinesService,
    private machinesMapperService: MachinesMapperService
  ) {}

  ngOnInit() {
    this.machines = this.machinesService.getMachines();
    this.displayedMachines = this.machines.map((el) =>
      this.machinesMapperService.mapMachineToMachineDisplayed(el)
    );
  }

  editMachine() {
    this.machinesService.editMachine(
      this.form.value.name!,
      this.form.value.note!
    );
  }
  activateDeactivateMachine() {
    this.machinesService.activateDeactivateMachine();
  }

  onActionChosen(event: { id: string; action: string }) {
    this.machinesService.action = event.action;
    this.machinesService.id = event.id;

    if (event.action == 'changePrices') {
      this.snacksInMachine = this.machinesService.getSnacks(event.id);
      this.snacksInMachineDisplayed = this.snacksInMachine.map((el) =>
        this.machinesMapperService.mapSnackInMachineToSnackInMachineDisplayed(
          el
        )
      );
      this.snacksInMachine.forEach((snack, index) => {
        const controlName = `snack_${index}`;
        this.snacksForm.addControl(
          controlName,
          this.fb.control(snack.price, Validators.required)
        );
      });
      this.showMachines = false;
      this.chosenMachineName = this.machinesService.getMachine(event.id).name;
    } else {
      this.setFormValuesToSelectedItem();
    }
  }

  setFormValuesToSelectedItem() {
    const machine = this.machinesService.getCurrentMachine();
    this.form.setValue({
      name: machine.name,
      note: machine.note,
    });
  }

  onCallbackCalled() {
    switch (this.machinesService.action) {
      case 'editMachine': {
        this.editMachine();
        break;
      }
      case 'activate/deactivateMachine': {
        this.activateDeactivateMachine();
        break;
      }
      case 'changePrices': {
        this.changePrices();
        break;
      }
    }
  }

  goBack() {
    this.showMachines = true;
  }

  changePrices() {
    if (this.snacksForm.valid) {
      // Gather the updated prices from the form and send them to the backend
      const updatedPrices = [];
      for (const controlName in this.snacksForm.controls) {
        updatedPrices.push({
          snackName: controlName,
          price: this.snacksForm.get(controlName)!.value,
        });
      }
      this.machinesService.changePricesInMachine(updatedPrices);
    }
  }
}
