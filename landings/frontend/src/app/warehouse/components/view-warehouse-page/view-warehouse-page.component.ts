import { Component, OnInit } from '@angular/core';
import { WarehouseService } from '../../services/warehouse.service';
import { WarehouseSnack } from '../../models/warehouseSnack.model';
import {WarehouseMapperService} from "../../services/warehouse-mapper.service";

@Component({
  selector: 'app-view-warehouse-page',
  templateUrl: './view-warehouse-page.component.html',
  styleUrls: ['./view-warehouse-page.component.scss'],
})
export class ViewWarehousePageComponent implements OnInit {
  columns = ['ID', 'Nazwa', 'Ilość'];

  snacks: WarehouseSnack[] = [];

  constructor(private warehouseService: WarehouseService, private warehouseMapperService: WarehouseMapperService) {}

  ngOnInit() {
    this.getSnacks()
  }

  getSnacks() {
    this.warehouseService.getSnacks().subscribe(snacksFromApi => this.snacks = snacksFromApi.map(snackFromApi => this.warehouseMapperService.mapWarehouseSnackFromApiToWarehouseSnack(snackFromApi)))
  }
}
