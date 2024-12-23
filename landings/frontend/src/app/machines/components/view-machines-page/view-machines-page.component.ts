import { Component, OnInit } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { MachinesService } from '../../services/machines.service';
import { Machine } from '../../models/machine.model';
import { MachinesMapperService } from '../../services/machines-mapper.service';
import { SnackInMachine } from '../../models/snack-in-machine.model';
import { SnackInMachineDisplayed } from '../../models/snack-in-machine-displayed.model';

@Component({
  selector: 'app-view-machines-page',
  templateUrl: './view-machines-page.component.html',
  styleUrls: ['./view-machines-page.component.scss'],
})
export class ViewMachinesPageComponent implements OnInit {
  machinesListcolumns = ['ID', 'Lokalizacja', 'Liczba pozycji', 'Pojemność'];
  snacksListcolumns = ['ID', 'Nazwa'];

  machines: Machine[] = [];


  snacksInMachine: SnackInMachine[] = [
    {
      id: '1',
      name: 'init snack',
      price: 69,
    },
  ];

  snacksInMachineDisplayed: SnackInMachineDisplayed[] = [];

  showMachines: boolean = true;

  chosenMachineLocation = '';

  machinesListButtons = [
    { text: 'Edytuj', action: 'editMachine' },
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
    location: ['', Validators.required],
    positionsNumber: ['', Validators.min(1)],
    positionsCapacity: ['', Validators.min(1)],
  });

  snacksForm = this.fb.group({});

  constructor(
    private fb: FormBuilder,
    private machinesService: MachinesService,
    private machinesMapperService: MachinesMapperService
  ) {}

  ngOnInit() {
this.getMachines()
  }

  getMachines() {
    this.machinesService.getMachines().subscribe((machinesFromApi) => this.machines = machinesFromApi.map(machineFromApi => this.machinesMapperService.mapMachineFromApiToMachine(machineFromApi)));
  }

  editMachine() {
    this.machinesService.editMachine(
      this.form.value.location!,
      this.form.value.positionsNumber!,
      this.form.value.positionsCapacity!
    );
  this.getMachines()
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
          this.fb.control(snack.price, [Validators.required, Validators.min(1)])
        );
      });
      this.showMachines = false;
      this.chosenMachineLocation = this.machinesService.getMachine(event.id).location;
    } else {
      this.setFormValuesToSelectedItem();
    }
  }

  setFormValuesToSelectedItem() {
    const machine = this.machinesService.getCurrentMachine();
    this.form.setValue({
      location: machine.location,
      positionsNumber: machine.positionsNumber,
      positionsCapacity: machine.positionsCapacity,
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
