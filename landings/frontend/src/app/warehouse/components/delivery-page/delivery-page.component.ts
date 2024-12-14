import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Snack } from 'src/app/snacks/models/snack/snack.model';
import { SnacksService } from 'src/app/snacks/services/snacks.service';
import { WarehouseService } from '../../services/warehouse.service';

@Component({
  selector: 'app-delivery-page',
  templateUrl: './delivery-page.component.html',
  styleUrls: ['./delivery-page.component.scss'],
})
export class DeliveryPageComponent implements OnInit {
  columns = ['ID', 'Nazwa'];

  inputs = [
    {
      title: 'Ilość do przyjęcia',
      name: 'snack',
      type: 'number',
    },
  ];

  snacks: Snack[] = [];

  form = this.fb.group({});

  constructor(
    private fb: FormBuilder,
    private snacksService: SnacksService,
    private warehouseService: WarehouseService
  ) {}

  ngOnInit(): void {
    this.snacks = this.snacksService.getSnacks();
    this.snacks.forEach((snack, index) => {
      const controlName = `snack_${index}`;
      this.form.addControl(
        controlName,
        this.fb.control('0', [Validators.min(0), Validators.required])
      );
    });
  }

  onSubmit() {
    const delivery = [];
    for (const controlName in this.form.controls) {
      delivery.push({
        snackName: controlName,
        amount: this.form.get(controlName)!.value,
      });
    }
    this.warehouseService.acceptDelivery(delivery);

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
