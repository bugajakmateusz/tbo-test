import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Snack } from '../../../snacks/models/snack/snack.model';
import { SnacksService } from '../../../snacks/services/snacks.service';
import { WarehouseService } from '../../services/warehouse.service';
import { AlertService } from '../../../shared/services/alert.service';
import {WarehouseMapperService} from "../../services/warehouse-mapper.service";

@Component({
  selector: 'app-delivery-page',
  templateUrl: './delivery-page.component.html',
  styleUrls: ['./delivery-page.component.scss'],
})
export class DeliveryPageComponent implements OnInit {
  columns = ['ID', 'Nazwa'];

  buttons = [
    { text: 'Wybierz', action: 'acceptSnack' },
  ];

  snacks: Snack[] = [];

  displayedSnacks: {id: string, name: string}[] = []

  form = this.fb.group({
    amount: ['', [Validators.required, Validators.min(1)]],
  });

  constructor(
    private fb: FormBuilder,
    private warehouseService: WarehouseService,
    private warehouseMapperService: WarehouseMapperService
  ) {}

  ngOnInit(): void {
    this.getSnacks()
  }

  getSnacks() {
    this.warehouseService.getSnacks().subscribe(snacksFromApi => {
      this.snacks = snacksFromApi.map(snackFromApi => this.warehouseMapperService.mapWarehouseSnackFromApiToWarehouseSnack(snackFromApi))
      this.displayedSnacks = this.snacks.map(snack => {return {id: snack.id, name: snack.name}})
    })
  }

  onActionChosen(event: { id: string; action: string }) {
    this.warehouseService.action = event.action;
    this.warehouseService.snackId = event.id;
    this.form.setValue({
      amount: ''
    })
  }

  acceptSnack() {
    this.warehouseService.acceptDelivery(Number(this.form.value.amount))
  }
}
