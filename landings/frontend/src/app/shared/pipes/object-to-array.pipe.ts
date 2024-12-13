import { Pipe, PipeTransform } from '@angular/core';

@Pipe({
  name: 'objectToArray',
})
export class ObjectToArrayPipe implements PipeTransform {
  transform(value: any): string[] {
    if (typeof value !== 'object') {
      throw new Error('Input value must be an object.');
    }
    return Object.values(value);
  }
}
