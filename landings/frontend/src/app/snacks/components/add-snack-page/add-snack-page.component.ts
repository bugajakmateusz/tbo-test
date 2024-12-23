import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { SnacksService } from '../../services/snacks.service';
import { MachinesService } from 'src/app/machines/services/machines.service';
import { Machine } from 'src/app/machines/models/machine.model';
import { AlertService } from 'src/app/shared/services/alert.service';

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
    }
  }

  showAlert() {
    this.alertService.showAlertForTime('successAlert', 2000);
  }
}
