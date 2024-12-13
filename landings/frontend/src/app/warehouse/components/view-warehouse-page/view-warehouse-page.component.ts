import { Component, OnInit } from '@angular/core';
import { WarehouseService } from '../../services/warehouse.service';
import { WarehouseSnack } from '../../models/warehouseSnack.model';

@Component({
  selector: 'app-view-warehouse-page',
  templateUrl: './view-warehouse-page.component.html',
  styleUrls: ['./view-warehouse-page.component.scss'],
})
export class ViewWarehousePageComponent implements OnInit {
  columns = ['ID', 'Nazwa', 'Ilość'];

  snacks: WarehouseSnack[] = [];

  constructor(private warehouseService: WarehouseService) {}

  ngOnInit() {
    this.snacks = this.warehouseService.getSnacks();
  }
}
