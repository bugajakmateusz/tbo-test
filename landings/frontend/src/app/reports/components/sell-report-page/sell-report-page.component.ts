import { Component } from '@angular/core';
import {Machine} from "../../../machines/models/machine.model";
import {MachineSimpleDisplayed} from "../../../machines/models/machine-simple-displayed.model";
import {FormBuilder, FormGroup, Validators} from "@angular/forms";
import {MachinesService} from "../../../machines/services/machines.service";
import {MachinesMapperService} from "../../../machines/services/machines-mapper.service";
import {ReportsService} from "../../services/reports.service";

@Component({
  selector: 'app-sell-report-page',
  templateUrl: './sell-report-page.component.html',
  styleUrls: ['./sell-report-page.component.scss']
})
export class SellReportPageComponent {
  columns = ['ID', 'Nazwa'];
  checkboxes = [
    {
      title: 'Wybierz',
      name: 'machine',
    },
  ];

  machines: Machine[] = [];

  machineIdMachineCheckboxDictionary: any[] = []

  displayedMachines: MachineSimpleDisplayed[] = [];

  showMachines: boolean = true;

  machinesSelectedForm = this.fb.group({});

  datesForm = this.fb.group({
    dateFrom: ['', Validators.required],
    dateTo: ['', Validators.required],
  });

  constructor(
      private fb: FormBuilder,
      private machinesService: MachinesService,
      private machinesMapperService: MachinesMapperService,
      private reportsService: ReportsService
  ) {}

  ngOnInit(): void {
    this.getMachines()
  }

  getMachines() {
    this.machinesService.getMachines().subscribe((machinesFromApi) => {
      this.machines = machinesFromApi.map(machineFromApi => this.machinesMapperService.mapMachineFromApiToMachine(machineFromApi))
      this.displayedMachines = this.machines.map(machine => this.machinesMapperService.mapMachineToMachineSimpleDisplayed(machine))
      this.machines.forEach((machine, index) => {
        const controlName = `machine_${index}`;
        this.machineIdMachineCheckboxDictionary.push({ [controlName]: machine.id })
        this.machinesSelectedForm.addControl(controlName, this.fb.control(false));
      });
    });
  }

  goNext() {
    if (!this.goNextButtonDisabled()) {
      this.resetDatesForm();
      this.showMachines = false;
    }
  }

  goBack() {
    this.resetMachinesForm();
    this.showMachines = true;
  }

  resetMachinesForm() {
    this.machinesSelectedForm = new FormGroup({});
    this.machines.forEach((machine, index) => {
      const controlName = `machine_${index}`;
      this.machinesSelectedForm.addControl(controlName, this.fb.control(false));
    });
  }

  resetDatesForm() {
    this.datesForm.setValue({
      dateFrom: '',
      dateTo: '',
    });
  }

  onSubmit() {
    if (!this.submitButtonDisabled()) {
      // console.log(this.machineIdMachineCheckboxDictionary)
      // console.log(this.machinesSelectedForm.value)
      const machines: any[] = []
      Object.values(this.machinesSelectedForm.value).forEach((value, index) => {
        if(value) {
          machines.push(Object.values(this.machineIdMachineCheckboxDictionary[index])[0])
        }
      })


      this.reportsService.createSellReport(
          this.datesForm.value.dateFrom!,
          this.datesForm.value.dateTo!, machines
      );
      this.goBack();
    }
  }

  goNextButtonDisabled(): boolean {
    return Object.values(this.machinesSelectedForm.value).every(
        (el) => el === false
    );
  }

  submitButtonDisabled(): boolean {
    return (
        !this.datesForm.valid ||
        this.datesForm.value.dateFrom! > this.datesForm.value.dateTo!
    );
  }
}
