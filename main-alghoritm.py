def generate_table():
    # Initialize values
    initial_value = 100000  # Starting value for the first column
    increment_rate = 1.1   # Multiplicative growth factor (10%)
    rows = 100             # Number of rows to generate

    # Create an empty table to hold the results
    table = []

    # Generate values for each row
    current_value = initial_value
    for day in range(1, rows + 1):
        table.append((f"{day}-kun", int(current_value)))
        current_value *= increment_rate  # Apply growth factor

    # Print the table
    for day, value in table:
        print(f"{day}: {value}")

# Call the function to generate and display the table
generate_table()