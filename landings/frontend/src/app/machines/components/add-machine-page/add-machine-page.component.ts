import { Component } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { MachinesService } from '../../services/machines.service';
import { AlertService } from 'src/app/shared/services/alert.service';

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
    private machinesService: MachinesService,
    private alertService: AlertService
  ) {}

  onSubmit() {
    if (this.form.valid) {
      this.machinesService.addMachine(
        this.form.value.name!,
        this.form.value.note!
      );
      this.form.reset();
      this.showAlert();
    }
  }

  showAlert() {
    this.alertService.showAlertForTime('successAlert', 2000);
  }
}
