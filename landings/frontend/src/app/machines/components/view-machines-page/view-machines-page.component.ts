import { Component, OnInit } from '@angular/core';
import { FormBuilder } from '@angular/forms';
import { MachinesService } from '../../services/machines.service';
import { Machine } from '../../models/machine.model';
import { MachineDisplayed } from '../../models/machine-displayed.model';
import { MachinesMapperService } from '../../services/machines-mapper.service';

@Component({
  selector: 'app-view-machines-page',
  templateUrl: './view-machines-page.component.html',
  styleUrls: ['./view-machines-page.component.scss'],
})
export class ViewMachinesPageComponent implements OnInit {
  columns = ['ID', 'Nazwa', 'Aktywna'];

  machines: Machine[] = [];

  displayedMachines: MachineDisplayed[] = [];

  buttons = [
    { text: 'Edytuj', action: 'editMachine' },
    { text: 'Aktywuj/Dezaktywuj', action: 'activate/deactivateMachine' },
  ];

  form = this.fb.group({
    name: [''],
    note: [''],
  });

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
    this.setFormValuesToSelectedItem();
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
      }
    }
  }
}
