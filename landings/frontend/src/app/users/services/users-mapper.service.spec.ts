import { TestBed } from '@angular/core/testing';

import { UsersMapperService } from './users-mapper.service';

describe('UsersMapperService', () => {
  let service: UsersMapperService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(UsersMapperService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
