import { Component } from '@angular/core';
import { WarehouseSnack } from '../../models/warehouseSnack.model';
import { WarehouseService } from '../../services/warehouse.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import {Machine} from "../../../machines/models/machine.model";
import {Snack} from "../../../snacks/models/snack/snack.model";
import {SnackInMachineDisplayed} from "../../../machines/models/snack-in-machine-displayed.model";
import {MachinesService} from "../../../machines/services/machines.service";
import {SnacksService} from "../../../snacks/services/snacks.service";
import {MachinesMapperService} from "../../../machines/services/machines-mapper.service";
import {SnacksMapperService} from "../../../snacks/services/snacks-mapper.service";
import {SnackInMachine2} from "../../../snacks/models/snack-in-machine-2.model";

@Component({
  selector: 'app-hand-to-courier-page',
  templateUrl: './hand-to-courier-page.component.html',
  styleUrls: ['./hand-to-courier-page.component.scss'],
})
export class HandToCourierPageComponent {
  machines: Machine[] = [];
  displayedMachines: {id: string, location: string}[] = []
  snacks: Snack[] = []
  snacksOptions: {name: string, value: string}[] = []
  snacksInMachineDisplayed: any[] = [];
  showMachines: boolean = true;
  chosenMachineLocation = '';

  chosenSnackToPutMoreAmountNow = 0

  machinesListcolumns = ['ID', 'Lokalizacja'];
  snacksListcolumns = ['ID', 'Nazwa', 'Ilość', 'Pozycja'];
  machinesListButtons = [
    {
      text: 'Wybierz',
      action: 'changePrices',
    },
  ];
  snacksListButtons = [
    { text: 'Dodaj', action: 'changePrice' }
  ];

  form = this.fb.group({
    location: ['', Validators.required],
    positionsNumber: ['', [Validators.required, Validators.min(1)]],
    positionsCapacity: ['', [Validators.required, Validators.min(1)]],
  });

  addSnackForm = this.fb.group({
    snackId: ['', Validators.required],
    amount: ['', [Validators.required, Validators.min(1)]],
    position: ['', [Validators.required, Validators.max(3)]]
  });

  putSnackForm = this.fb.group({
    amount: ['', [Validators.required, Validators.min(1)]],
  });

  constructor(
    private fb: FormBuilder,
    private machinesService: MachinesService,
    private snacksService: SnacksService,
    private machinesMapperService: MachinesMapperService,
    private snacksMapperService: SnacksMapperService
  ) {}

  ngOnInit() {
    this.getMachines()
  }

  getMachines() {
    this.machinesService.getMachines().subscribe((machinesFromApi) => {
      this.machines = machinesFromApi.map(machineFromApi => this.machinesMapperService.mapMachineFromApiToMachine(machineFromApi))
      this.displayedMachines = this.machines.map(machine => { return { id: machine.id, location: machine.location } })
    });
  }

  getSnacksOptions() {
    this.snacksService.getSnacks().subscribe(snacksFromApi => {
      this.snacks = snacksFromApi.map(snackFromApi => this.snacksMapperService.mapSnackFromApiToSnack(snackFromApi))
      const filteredSnacksOptions = this.snacks.filter(option => !this.snacksInMachineDisplayed.some(snackInMachine => {
        return option.id === snackInMachine.id
      }));
      this.snacksOptions = filteredSnacksOptions.map(option => {return {name: `${option.id} - ${option.name}`, value: option.id}})
    })
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
      this.machinesService.machineSnackId = event.id
      this.getCurrentMachineData()
      this.showMachines = false;
    } else {
      this.setFormValuesToSelectedItem();
    }
  }

  getCurrentMachineData() {

    this.machinesService.getMachineWithSnacksFromApi().subscribe(data => {
        this.chosenMachineLocation = data.data.attributes.location
      console.log(data)
        this.snacksInMachineDisplayed = []
      data.included.filter((el:any) => el.type === "snacks").forEach((snack:any) => {
        let newSnack: SnackInMachine2 = {id: snack.id, name: snack.attributes.name, amount: "1", position: "pos"}
        this.snacksInMachineDisplayed.push(newSnack)
      })
      data.included.filter((el:any) => el.type === "machine-snacks").forEach((machineSnack:any) => {
        this.snacksInMachineDisplayed.find(snack => snack.id === machineSnack.relationships.snack.data.id)!.position = machineSnack.attributes.position
        this.snacksInMachineDisplayed.find(snack => snack.id === machineSnack.relationships.snack.data.id)!.amount = machineSnack.attributes.quantity
        })
      this.getSnacksOptions()
    })
  }

  onSnackToChangePriceChosen(event: { id: string; action: string }) {
    this.machinesService.action = event.action;
    this.machinesService.snackInMachineId = event.id;
    this.snacksService.id = event.id
    this.putSnackForm.setValue({
      amount: ""
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
      this.machinesService.putNewSnackToMachine(this.addSnackForm.value.snackId!, this.addSnackForm.value.amount!, this.addSnackForm.value.position!)
      this.addSnackForm.reset()
      this.getCurrentMachineData()
    }
  }

  putMoreSnack() {
    if(this.putSnackForm.valid) {
      this.machinesService.putMoreSnacksToMachine(Number(this.putSnackForm.value.amount!), this.chosenSnackToPutMoreAmountNow)
      this.getCurrentMachineData()
      this.putSnackForm.reset()
    }
  }

  goBack() {
    this.showMachines = true;
  }

}
