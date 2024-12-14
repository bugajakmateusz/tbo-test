import { Component, OnInit } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { MachineDisplayed } from 'src/app/machines/models/machine-displayed.model';
import { MachineSimpleDisplayed } from 'src/app/machines/models/machine-simple-displayed.model';
import { Machine } from 'src/app/machines/models/machine.model';
import { MachinesMapperService } from 'src/app/machines/services/machines-mapper.service';
import { MachinesService } from 'src/app/machines/services/machines.service';
import { ReportsService } from '../../services/reports.service';

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

  displayedMachines: MachineSimpleDisplayed[] = [];

  showMachines: boolean = true;

  machinesSelectedForm = this.fb.group({});

  datesForm = this.fb.group({
    dateFrom: [''],
    dateTo: [''],
  });

  constructor(
    private fb: FormBuilder,
    private machinesService: MachinesService,
    private machinesMapperService: MachinesMapperService,
    private reportsService: ReportsService
  ) {}

  ngOnInit(): void {
    this.machines = this.machinesService.getMachines();
    this.displayedMachines = this.machines.map((el) =>
      this.machinesMapperService.mapMachineToMachineSimpleDisplayed(el)
    );
    this.machines.forEach((machine, index) => {
      const controlName = `machine_${index}`;
      this.machinesSelectedForm.addControl(controlName, this.fb.control(false));
    });
  }

  goNext() {
    this.showMachines = false;
  }

  goBack() {
    this.showMachines = true;
  }

  onSubmit() {
    this.reportsService.createMachinesReport(
      this.datesForm.value.dateFrom!,
      this.datesForm.value.dateTo!
    );
  }
}
