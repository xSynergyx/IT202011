CREATE TABLE Names
(
    id             int auto_increment,
    user_id        int not null,
    first_name     varchar(30),
    last_name      varchar(30),
    created        datetime default current_timestamp,
    primary key (id),
    foreign key (user_id) references Users (id)
)
