import { Component } from '@angular/core';
import { WarehouseSnack } from '../../models/warehouseSnack.model';
import { WarehouseService } from '../../services/warehouse.service';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-hand-to-courier-page',
  templateUrl: './hand-to-courier-page.component.html',
  styleUrls: ['./hand-to-courier-page.component.scss'],
})
export class HandToCourierPageComponent {
  columns = ['ID', 'Nazwa', 'Ilość w magazynie'];

  snacks: WarehouseSnack[] = [];

  inputs = [
    {
      title: 'Ilość do wydania',
      name: 'snack',
      type: 'number',
    },
  ];

  form = this.fb.group({});

  constructor(
    private fb: FormBuilder,
    private warehouseService: WarehouseService
  ) {}

  ngOnInit() {
    // this.snacks = this.warehouseService.getSnacks();
    this.snacks.forEach((snack, index) => {
      const controlName = `snack_${index}`;
      this.form.addControl(
        controlName,
        this.fb.control('0', [Validators.min(0), Validators.required])
      );
    });
  }

  onSubmit() {
    const snacksHanded = [];
    for (const controlName in this.form.controls) {
      snacksHanded.push({
        snackName: controlName,
        amount: this.form.get(controlName)!.value,
      });
    }
    this.warehouseService.handToCourier(snacksHanded);

    this.form = new FormGroup({});
    this.snacks.forEach((snack, index) => {
      const controlName = `snack_${index}`;
      this.form.addControl(
        controlName,
        this.fb.control('0', [Validators.min(0), Validators.required])
      );
    });
  }

  buttonDisabled(): boolean {
    return (
      Object.values(this.form.value).every((el) => el === '0') ||
      !this.form.valid
    );
  }
}
