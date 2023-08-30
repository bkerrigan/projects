require 'csv'

class SecondLowestCostSilverPlan
  def initialize(slcsp:, plans:, zips: )
    @slcsp_file, @plans_file, @zips_file = slcsp, plans, zips

    # Parse zips file and build hash with zip code to plan area
    @zips = parse_zips_file()

    # Parse plans file to build hash with plan area as key with a list of all the available plans
    @plans = parse_plans_file()
  end

  def find_slcsp
    # Iterate through the slcsp file and look up the zip code and plan information to find the slcsp
    results = []
    begin
      slcsp_file = CSV.parse(File.read(@slcsp_file), headers: true)
      slcsp_file.each do |row|
        zip = row.fetch('zipcode')
        rate = ''
        zip_info = @zips[zip]
        if zip_info&.uniq&.size == 1
          rate_area = zip_info.first
          rates = @plans[rate_area]&.uniq&.sort
          if rates&.size.to_i > 1
            rate = rates[1]
          end
        end
        results << [zip, rate]
        #puts "#{zip}, #{rate}"
      end
    rescue Errno::ENOENT
      STDERR.puts "No SLCSP CSV file found"
    end
    results
  end

  def parse_zips_file
    zip_codes = {}
    begin
      zip_file = CSV.parse(File.read(@zips_file), headers: true)
      zip_file.each do |row|
        zip_code = row.fetch('zipcode')
        rate_area = "#{row.fetch('state')}-#{row.fetch('rate_area')}"
        if zip_codes.key?(zip_code)
          zip_codes[zip_code] << rate_area
        else
          zip_codes[zip_code] = [rate_area]
        end
      end
    rescue Errno::ENOENT
      STDERR.puts "No Zips CSV file found"
    end

    zip_codes
  end

  def parse_plans_file
    plans = {}
    begin
      plan_file = CSV.parse(File.read(@plans_file), headers: true)
      plan_file.each do |row|
        next if row.fetch('metal_level').downcase != 'silver'

        rate_area = "#{row.fetch('state')}-#{row.fetch('rate_area')}"
        rate = row.fetch('rate')
        if plans.key?(rate_area)
          plans[rate_area] << rate
        else
          plans[rate_area] = [rate]
        end
      end
    rescue Errno::ENOENT
      STDERR.puts "No Plans CSV file found"
    end

    plans
  end
end
