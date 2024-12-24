import { Component, OnInit } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { TableComponent } from '../../../shared/components/table/table.component';
import { SnacksService } from '../../services/snacks.service';
import { Snack } from '../../models/snack/snack.model';
import {SnacksMapperService} from "../../services/snacks-mapper.service";

@Component({
  selector: 'app-view-snacks-page',
  templateUrl: './view-snacks-page.component.html',
  styleUrls: ['./view-snacks-page.component.scss'],
})
export class ViewSnacksPageComponent implements OnInit {
  columns = ['ID', 'Nazwa'];

  snacks: Snack[] = [];

  buttons = [
    { text: 'Edytuj', action: 'editSnack' },
  ];

  form = this.fb.group({
    name: ['', Validators.required],
  });

  constructor(private fb: FormBuilder, private snacksService: SnacksService, private snacksMapperService: SnacksMapperService) {}

  ngOnInit() {
    this.getSnacks()
  }

  getSnacks(){
    this.snacksService.getSnacks().subscribe(snacksFromApi => this.snacks = snacksFromApi.map(snackFromApi => this.snacksMapperService.mapSnackFromApiToSnack(snackFromApi)))

  }

  editSnack() {
    this.snacksService.editSnack(this.form.value.name!);
    this.getSnacks()
  }

  onActionChosen(event: { id: string; action: string }) {
    this.snacksService.action = event.action;
    this.snacksService.id = event.id;
    this.setFormValuesToSelectedItem();
  }

  setFormValuesToSelectedItem() {
    const snack = this.snacksService.getCurrentSnack();
    this.form.setValue({
      name: snack.name,
    });
  }

  onCallbackCalled() {
    switch (this.snacksService.action) {
      case 'editSnack': {
        this.editSnack();
        break;
      }
    }
  }
}
