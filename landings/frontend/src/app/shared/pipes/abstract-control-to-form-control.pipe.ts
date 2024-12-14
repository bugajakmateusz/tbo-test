import { Pipe, PipeTransform } from '@angular/core';
import { Form, FormControl } from '@angular/forms';

@Pipe({
  name: 'abstractControlToFormControl',
})
export class AbstractControlToFormControlPipe implements PipeTransform {
  transform(value: any): FormControl<string | null> {
    return value as FormControl;
  }
}
