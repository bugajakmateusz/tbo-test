import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { SnacksService } from '../../services/snacks.service';
import { MachinesService } from '../../../machines/services/machines.service';
import { Machine } from '../../../machines/models/machine.model';
import { AlertService } from '../../../shared/services/alert.service';

@Component({
  selector: 'app-add-snack-page',
  templateUrl: './add-snack-page.component.html',
  styleUrls: ['./add-snack-page.component.scss'],
})
export class AddSnackPageComponent implements OnInit {

  form = this.fb.group({
    name: ['', Validators.required],
  });


  constructor(
    private fb: FormBuilder,
    private snacksService: SnacksService,
    private alertService: AlertService
  ) {}

  ngOnInit(): void {

  }


  onSubmit() {
    if (this.form.valid) {
      this.snacksService.addSnack(this.form.value.name!);
      this.showAlert();
      this.form.reset()
    }
  }

  showAlert() {
    this.alertService.showAlertForTime('successAlert', 2000);
  }
}
