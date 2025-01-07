import {Component, OnInit} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {MachineSimpleDisplayed} from '../../../machines/models/machine-simple-displayed.model';
import {Machine} from '../../../machines/models/machine.model';
import {MachinesMapperService} from '../../../machines/services/machines-mapper.service';
import {MachinesService} from '../../../machines/services/machines.service';
import {ReportsService} from '../../services/reports.service';

@Component({
  selector: 'app-machine-report-page',
  templateUrl: './machine-report-page.component.html',
  styleUrls: ['./machine-report-page.component.scss'],
})
export class MachineReportPageComponent implements OnInit {
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
  ) {
  }

  ngOnInit(): void {
    this.getMachines()
  }

  getMachines() {
    this.machinesService.getMachines().subscribe((machinesFromApi) => {
      this.machines = machinesFromApi.map(machineFromApi => this.machinesMapperService.mapMachineFromApiToMachine(machineFromApi))
      this.displayedMachines = this.machines.map(machine => this.machinesMapperService.mapMachineToMachineSimpleDisplayed(machine))
      this.machines.forEach((machine, index) => {
        const controlName = `machine_${index}`;
        this.machineIdMachineCheckboxDictionary.push({[controlName]: machine.id})
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
    console.log("report pls")
    // console.log(this.machinesSelectedForm.value)
    const machines: any[] = []


    Object.values(this.machinesSelectedForm.value).forEach((value, index) => {
      if (value) {
        machines.push(Object.values(this.machineIdMachineCheckboxDictionary[index])[0])
      }
    })
    this.goBack();
  }

  goNextButtonDisabled(): boolean {
    return Object.values(this.machinesSelectedForm.value).every(
      (el) => el === false
    );
  }

  submitButtonDisabled(): boolean {
    return (
      !this.datesForm.valid);
  }
}
