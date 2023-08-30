require 'optparse'
require 'ostruct'

require_relative './slcsp.rb'

options = OpenStruct.new
OptionParser.new do |opt|
  opt.on('-p', '--plans PLANS', 'Path for the file with health care plan information') { |o| options.plans = o }
  opt.on('-s', '--slcsp SLCSP', 'The file to look up the SLCSP for the given zip codes') { |o| options.slcsp = o }
  opt.on('-z', '--zips ZIPS', 'Path for the file with zip code information') { |o| options.zips = o }
end.parse!

slcsp = SecondLowestCostSilverPlan.new(slcsp: options.slcsp, plans: options.plans, zips: options.zips).find_slcsp()

puts "zipcode,rate"
slcsp.each do |row|
  rate = if row[1] == ''
           ''
         else
           sprintf('%.2f', row[1].to_f)
         end
  puts "#{row[0]},#{rate}"
end
