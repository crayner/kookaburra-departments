ALTER TABLE __prefix__DepartmentResource
    ADD CONSTRAINT FOREIGN KEY (department) REFERENCES __prefix__Department (id);
ALTER TABLE __prefix__DepartmentStaff
    ADD CONSTRAINT FOREIGN KEY (department) REFERENCES __prefix__Department (id),
    ADD CONSTRAINT FOREIGN KEY (person) REFERENCES __prefix__Person (id);
