import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormGroup } from '@angular/forms';

@Component({
  selector: 'app-table',
  templateUrl: './table.component.html',
  styleUrls: ['./table.component.scss'],
})
export class TableComponent implements OnInit {
  @Input() title = '';
  @Input({ required: true }) columns!: string[];
  @Input({ required: true }) rows!: any[];
  @Input() buttons: {
    text: string;
    action: string;
  }[] = [];
  @Input() formGroup: FormGroup = new FormGroup({});
  @Input() inputs: {
    type: string;
    name: string;
    title: string;
  }[] = [];
  @Input() checkboxes: {
    name: string;
    title: string;
  }[] = [];
  @Input() min: number = -1000000;
  @Input() max: number = 1000000;

  @Output() actionChosen = new EventEmitter<{
    action: string;
    id: string;
  }>();

  ngOnInit(): void {}

  chooseAction(action: string, id: string) {
    this.actionChosen.emit({ action, id });
  }

  paintRed(row: any) {
    if(Object.keys(row).includes("amount") && (row.amount === "0" || row.amount === 0)) {
      return "table-danger"
    }
    return ""
  }
}
