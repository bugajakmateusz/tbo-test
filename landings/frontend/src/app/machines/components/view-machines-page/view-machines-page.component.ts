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
      this.getCurrentMachineData()
      this.showMachines = false;
    } else {
      this.setFormValuesToSelectedItem();
    }
  }

  getCurrentMachineData() {
    this.machinesService.getMachineFromApi().subscribe(data => {
      this.chosenMachineLocation = data.data.attributes.location
      this.snacksInMachineDisplayed = []
      data.included.filter((el:any) => el.type === "snacks").forEach((snack:any) => {
        let newSnack: SnackInMachineDisplayed = {id: snack.id, name: snack.attributes.name, price: "1"}
        this.snacksInMachineDisplayed.push(newSnack)
      })
      data.included.filter((el:any) => el.type === "snacks-prices").forEach((snackPrice:any) => {
        this.snacksInMachineDisplayed.find(snack => snack.id === snackPrice.relationships.snack.data.id)!.price = snackPrice.attributes.price
      })
    })
  }

  onSnackToChangePriceChosen(event: { id: string; action: string }) {
    this.machinesService.action = event.action;
    this.machinesService.snackInMachineId = event.id;
    this.snacksService.id = event.id
    this.changePriceForm.setValue({
      price: this.machinesService.getCurrentSnackPrice().price
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
      this.machinesService.addSnackToMachine(this.addSnackForm.value.snackId!, this.addSnackForm.value.price!)
      this.addSnackForm.reset()
    }
  }

  changePrice() {
    if(this.changePriceForm.valid) {
      this.machinesService.changePrice(this.changePriceForm.value.price!)
      this.getCurrentMachineData()
      this.changePriceForm.reset()
    }
  }

  goBack() {
    this.showMachines = true;
  }

}
