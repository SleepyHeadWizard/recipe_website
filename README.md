erDiagram
    users {
        int user_id PK "Auto Increment"
        varchar(255) username "Not Null, Unique"
        varchar(255) email "Not Null, Unique"
        varchar(255) password "Not Null"
    }
    recipes {
        int recipe_id PK "Auto Increment"
        int user_id FK
        varchar(255) title "Not Null"
        text description
        text ingredients "Not Null"
        text instructions "Not Null"
        int prep_time
        int cook_time
        int total_time
        varchar(255) image_path
    }
    users ||--o{ recipes : "user_id"
