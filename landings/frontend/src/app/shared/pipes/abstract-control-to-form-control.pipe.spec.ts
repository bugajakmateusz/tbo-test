import { AbstractControlToFormControlPipe } from './abstract-control-to-form-control.pipe';

describe('AbstractControlToFormControlPipe', () => {
  it('create an instance', () => {
    const pipe = new AbstractControlToFormControlPipe();
    expect(pipe).toBeTruthy();
  });
});
