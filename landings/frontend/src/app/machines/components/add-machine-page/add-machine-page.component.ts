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
    location: ['', Validators.required],
    positionsNumber: ['', Validators.min(1)],
    positionsCapacity: ['', Validators.min(1)],
  });
  constructor(
    private fb: FormBuilder,
    private machinesService: MachinesService,
    private alertService: AlertService
  ) {}

  onSubmit() {
    if (this.form.valid) {
      this.machinesService.addMachine(
        this.form.value.location!,
        this.form.value.positionsNumber!,
        this.form.value.positionsCapacity!
      );
      this.form.reset();
      this.showAlert();
    }
  }

  showAlert() {
    this.alertService.showAlertForTime('successAlert', 2000);
  }
}
