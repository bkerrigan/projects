require 'test/unit'
require_relative '../slcsp.rb'

class SecondLowestCostSilverPlanTest < Test::Unit::TestCase
  def setup
    @slcsp = SecondLowestCostSilverPlan.new(slcsp: File.join(File.dirname(__FILE__), 'test_files/slcsp_test.csv'),
                                            plans: File.join(File.dirname(__FILE__),'test_files/plans_test.csv'),
                                            zips: File.join(File.dirname(__FILE__),'test_files/zips_test.csv'))
  end

  def test_zips_parsing
    zips = @slcsp.parse_zips_file()

    assert_equal(3, zips.size)
  end

  def test_plans_parsing
    plans = @slcsp.parse_plans_file()
    assert_equal(3, plans.size)
  end

  def test_slcsp
    slcsp = @slcsp.find_slcsp()
    assert_equal(3, slcsp.size)

    # Found SLCSP
    assert_equal('54601', slcsp[0][0])
    assert_equal('495.53', slcsp[0][1])

    # No SLCSP present
    assert_equal('53711', slcsp[1][0])
    assert_equal('', slcsp[1][1])

    # Zip code in multiple rate areas
    assert_equal('54844', slcsp[2][0])
    assert_equal('', slcsp[2][1])
  end
end
