import { Component } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { MachinesService } from '../../services/machines.service';

@Component({
  selector: 'app-add-machine-page',
  templateUrl: './add-machine-page.component.html',
  styleUrls: ['./add-machine-page.component.scss'],
})
export class AddMachinePageComponent {
  form = this.fb.group({
    name: ['', Validators.required],
    note: [''],
  });
  constructor(
    private fb: FormBuilder,
    private machinesService: MachinesService
  ) {}

  onSubmit() {
    this.machinesService.addMachine(
      this.form.value.name!,
      this.form.value.note!
    );
  }
}
