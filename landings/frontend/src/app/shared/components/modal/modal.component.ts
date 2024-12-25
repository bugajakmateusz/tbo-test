import { Component, EventEmitter, Input, Output } from '@angular/core';
import { FormGroup } from '@angular/forms';

@Component({
  selector: 'app-modal',
  templateUrl: './modal.component.html',
  styleUrls: ['./modal.component.scss'],
})
export class ModalComponent {
  @Input({ required: true }) modalId!: string;
  @Input() title: string = "";
  @Input({ required: true }) noButtonText!: string;
  @Input({ required: true }) yesButtonText!: string;
  @Input() disabled: boolean = false;
  @Input() noButtons: boolean = false
  @Output() callbackCalled = new EventEmitter<any>();

  callCallback() {
    this.callbackCalled.emit();
  }
}
