import { Component, OnInit } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { MachinesService } from '../../services/machines.service';
import { Machine } from '../../models/machine.model';
import { MachinesMapperService } from '../../services/machines-mapper.service';
import { SnackInMachine } from '../../models/snack-in-machine.model';
import { SnackInMachineDisplayed } from '../../models/snack-in-machine-displayed.model';
import {SnacksService} from "../../../snacks/services/snacks.service";

@Component({
  selector: 'app-view-machines-page',
  templateUrl: './view-machines-page.component.html',
  styleUrls: ['./view-machines-page.component.scss'],
})
export class ViewMachinesPageComponent implements OnInit {
  machinesListcolumns = ['ID', 'Lokalizacja', 'Liczba pozycji', 'Pojemność'];
  snacksListcolumns = ['ID', 'Nazwa', 'Cena'];

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

  snacksListButtons = [
    { text: 'Zmień', action: 'changePrice' }
  ];

  form = this.fb.group({
    location: ['', Validators.required],
    positionsNumber: ['', [Validators.required, Validators.min(1)]],
    positionsCapacity: ['', [Validators.required, Validators.min(1)]],
  });

  addSnackForm = this.fb.group({
    snackId: ['', Validators.required],
    price: ['', [Validators.required, Validators.min(1)]],
  });

  changePriceForm = this.fb.group({
    price: ['', [Validators.required, Validators.min(1)]],
  });

  constructor(
    private fb: FormBuilder,
    private machinesService: MachinesService,
    private snacksService: SnacksService,
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
      this.showMachines = false;
      this.chosenMachineLocation = this.machinesService.getMachine(event.id).location;
    } else {
      this.setFormValuesToSelectedItem();
    }
  }

  onSnackToChangePriceChosen(event: { id: string; action: string }) {
    this.machinesService.action = event.action;
    this.machinesService.snackInMachineId = event.id;
    this.snacksService.id = event.id
    this.changePriceForm.setValue({
      price: ""
    })
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
    }
  }

  addSnack() {
    if(this.addSnackForm.valid) {
      console.log("add new snack", this.addSnackForm.value.snackId, this.addSnackForm.value.price)
      this.addSnackForm.reset()
    }
  }

  changePrice() {
    if(this.changePriceForm.valid) {
      console.log("change price", this.changePriceForm.value.price, "machine id:", this.machinesService.id, "snack id:", this.machinesService.snackInMachineId)
      this.changePriceForm.reset()
    }
  }

  goBack() {
    this.showMachines = true;
  }

}
